@section('style')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.2/dist/sweetalert2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection

@section('script')
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.2/dist/sweetalert2.min.js"
            type="text/javascript"></script>
    <script>
        function deleted(url, id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be delete this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(url)
                        .then(function (response) {
                            if (response.status == 200) {
                                $('#friend_' + id).remove();
                            } else {
                                Swal.fire(
                                    'Good job!',
                                    'error'
                                )
                            }
                        })
                        .catch(function (error) {
                            Swal.fire(
                                'Good job!',
                                'Something went wrong!',
                                'error'
                            )
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Your imaginary file is safe :)',
                        'error'
                    )
                }
            })
        }


        function action(url, id) {
            axios.get(url)
                .then(function (response) {
                    // handle success
                    console.log(response);
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                })
        }

    </script>
@endsection

<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl grid grid-cols-3 gap-16 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white col-span-1 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Friend List
                    <form method="get" action="{{route('dashboard')}}"><input type="text" name="query">
                        <button type="submit">search</button>
                    </form>
                </div>
                <div class="grid gap-4 p-6">

                    @foreach($friendList as $friend)
                        <div class="flex items-center" id="friend_{{$friend->id}}">
                            <div class="">{{$friend->first_name.' '.$friend->last_name}}</div>
                            <button class="text-sm bg-gray-400 text-white px-3 py-1 hover:bg-gray-500 rounded ml-6"
                                    onclick="deleted('{{route('friends.remove',$friend->id)}}',{{$friend->id}})">
                                Unfriend
                            </button>
                        </div>
                    @endforeach
                </div>
                {{ $friendList->links() }}
            </div>

            <div class="bg-white col-span-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>

                <div class="py-12">
                    <div class="max-w-7xl grid grid-cols-2 gap-16 mx-auto sm:px-6 lg:px-8">
                        <div class="bg-white col-span-1 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="grid gap-4 p-6">
                                @foreach($friendRequestList as $friendRequest)
                                    <div class="flex items-center" id="request_{{$friendRequest->id}}">
                                        <div class="">{{$friendRequest->first_name.' '.$friendRequest->last_name}}</div>
                                        <button
                                            class="text-sm bg-gray-400 text-white px-3 py-1 hover:bg-gray-500 rounded ml-6"
                                            onclick="action('{{route('friends.accept',$friendRequest->id)}}',{{$friendRequest->id}})">
                                            Accept
                                        </button>
                                        <button
                                            class="text-sm bg-gray-400 text-white px-3 py-1 hover:bg-gray-500 rounded ml-6"
                                            onclick="action('{{route('friends.deny',$friendRequest->id)}}',{{$friendRequest->id}})">
                                            Reject
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white col-span-1 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="grid gap-4 p-6">
                                <form action="{{route('friends.invite')}}" method="post">
                                    @csrf
                                    <div class="flex items-center">
                                        <div class="">
                                            <input type="text" name="email">
                                            @error('email')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit"
                                                class="text-sm bg-gray-400 text-white px-3 py-1 hover:bg-gray-500 rounded ml-6">
                                            Sent Invitation
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

